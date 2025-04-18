import requests
import string
import urllib.parse
import crypt
import sys

# Target URL
target_url = "http://cryptofailures.thm/index.php"

# Possible characters
charset = (
    string.ascii_lowercase +
    string.ascii_uppercase +
    string.digits +
    "!\"#$%&'()*+,-./:;<=>?@[\\]^_`{|}~"
)

# Initial User-Agent
user_agent = "A" * 256
known_prefix = "guest:" + user_agent + ":"
test_string = known_prefix[-8:]

def get_secure_cookie(user_agent):
    headers = {
        "Host": "cryptofailures.thm",
        "User-Agent": user_agent,
        "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8",
        "Accept-Language": "en-US,en;q=0.5",
        "Accept-Encoding": "gzip, deflate, br",
        "Connection": "close",
        "Upgrade-Insecure-Requests": "1"
    }

    try:
        response = requests.get(target_url, headers=headers, allow_redirects=False)
    except Exception as e:
        print(f"❌ Error fetching URL: {e}")
        return None

    cookies = response.cookies.get_dict()
    if "secure_cookie" not in cookies:
        print("❌ secure_cookie not found in response!")
        return None

    decoded_cookie = urllib.parse.unquote(cookies["secure_cookie"])

    # Ensure no remaining URL-encoded chars
    if "%" in decoded_cookie:
        print("❌ URL-encoded characters detected in secure_cookie!")
        sys.exit(1)

    return decoded_cookie

# Start brute-force
found_text = ""

while True:
    print(f"\nCurrent known part: {found_text}")

    # Get secure_cookie
    secure_cookie = get_secure_cookie(user_agent)
    user_agent = user_agent[1:]  # Slide window
    if not secure_cookie:
        print("❌ Failed to retrieve secure_cookie!")
        break

    print(f"✅ Retrieved (Decoded) secure_cookie: {secure_cookie}")
    salt = secure_cookie[:2]

    # Brute-force next character
    found_char = None
    for char in charset:
        test_string_temp = test_string[1:] + char
        print(test_string_temp)

        hashed_test = crypt.crypt(test_string_temp, salt)

        if hashed_test in secure_cookie:
            print(f"✅ Found character: {char}")
            found_text += char
            test_string = test_string_temp
            print(test_string)
            print(hashed_test)
            break
