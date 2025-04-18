import crypt

str_value = "admin:Mo"
salt = "C8"

hashed = crypt.crypt(str_value, salt)

print(hashed)
