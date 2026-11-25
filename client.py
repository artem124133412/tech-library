import getpass
import os
import re
import secrets
import sys
from html.parser import HTMLParser
import requests

if hasattr(sys.stdout, "reconfigure"):
    sys.stdout.reconfigure(encoding="utf-8")

BASE_URL = "http://127.0.0.1:8000"


class EasyAdminTopicParser(HTMLParser):
    def __init__(self) -> None:
        super().__init__()
        self.in_name_cell = False
        self.names: list[str] = []

    def handle_starttag(self, tag: str, attrs: list[tuple[str, str | None]]) -> None:
        attr_dict = dict(attrs)
        if tag == "td" and attr_dict.get("data-column") == "name":
            self.in_name_cell = True

    def handle_endtag(self, tag: str) -> None:
        if tag == "td" and self.in_name_cell:
            self.in_name_cell = False

    def handle_data(self, data: str) -> None:
        if self.in_name_cell:
            text = data.strip()
            if text:
                self.names.append(text)


def check_public_api() -> None:
    print("=== 1. ПЕРЕВІРКА ПУБЛІЧНОГО JSON API ===")

    r = requests.get(f"{BASE_URL}/api/topics", timeout=5)
    print(f"GET {r.url} -> HTTP {r.status_code}")
    for topic in r.json():
        print(f"  - {topic['name']} ({topic['slug']}): {topic['books_count']} книг")

    print("\nЗапит книг у розділі 'backend':")
    r_topic = requests.get(f"{BASE_URL}/api/topics/backend", timeout=5)
    print(f"GET {r_topic.url} -> HTTP {r_topic.status_code}")
    for book in r_topic.json()["books"]:
        print(f"  [{book['isbn']}] {book['title']} — {book['author']} ({book['published_year']}, {book['status']})")

    print("\nПеревірка неіснуючого розділу (очікуємо 404):")
    r_404 = requests.get(f"{BASE_URL}/api/topics/unknown", timeout=5)
    print(f"GET {r_404.url} -> HTTP {r_404.status_code} | {r_404.json()['error']}\n")


def check_admin_session() -> None:
    print("=== 2. ВХІД В АДМІНКУ ЧЕРЕЗ СЕСІЮ ===")
    answer = input("Перевірити вхід в /admin? (y/n): ").strip().lower()
    if answer != "y":
        return

    email = os.getenv("ADMIN_EMAIL") or input("Email адміністратора [admin@library.test]: ").strip() or "admin@library.test"
    password = os.getenv("ADMIN_PASSWORD") or getpass.getpass("Пароль адміністратора: ")

    session = requests.Session()

    r_admin = session.get(f"{BASE_URL}/admin", allow_redirects=False, timeout=5)
    print(f"1) GET /admin -> HTTP {r_admin.status_code} (Location: {r_admin.headers.get('Location')})")

    r_login_page = session.get(f"{BASE_URL}/login", timeout=5)
    match = re.search(r'name="_csrf_token"\s+value="([^"]+)"', r_login_page.text)
    raw_csrf = match.group(1) if match else "csrf-token"

    if raw_csrf == "csrf-token":
        csrf_token = secrets.token_urlsafe(18)
        session.cookies.set(f"csrf-token_{csrf_token}", "csrf-token", domain="127.0.0.1", path="/")
    else:
        csrf_token = raw_csrf

    r_post = session.post(
        f"{BASE_URL}/login",
        data={
            "_username": email,
            "_password": password,
            "_csrf_token": csrf_token,
        },
        allow_redirects=False,
        timeout=5,
    )
    print(f"2) POST /login -> HTTP {r_post.status_code} (Location: {r_post.headers.get('Location')})")

    r_topic_admin = session.get(f"{BASE_URL}/admin/topic", timeout=5)
    print(f"3) GET /admin/topic -> HTTP {r_topic_admin.status_code}")

    if r_topic_admin.status_code == 200 and "login" not in r_topic_admin.url:
        parser = EasyAdminTopicParser()
        parser.feed(r_topic_admin.text)
        print("Успішний вхід! Розділи з таблиці EasyAdmin:", parser.names)
    else:
        print("Не вдалося увійти. Перевірте email, пароль або роль ROLE_ADMIN.")


if __name__ == "__main__":
    check_public_api()
    check_admin_session()