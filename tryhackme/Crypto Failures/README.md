# Crypto Failures
> The scripts made in this CTF were made in python but as my version of python did not support the crypt cryptographic function and I could not use docker, I did some research and converted the scripts to php where the function is implemented!

## About the Challenge
Implementing your own military-grade encryption is usually not the best idea.

## 1st Flag
As always I started by running an nmap scan on the target IP address.
![image](https://github.com/user-attachments/assets/c31eafd6-6141-48e5-b184-dfe6169d8321)
This gives me the information that there are 2 open ports, 22 (SSH) and 80 (HTTP), which tells me that a website is running on the target.

Entering the website that is running we come across this page that gives us a message that seems to refer to some type of encryption:
![image](https://github.com/user-attachments/assets/d1e87654-d9e2-454e-bed0-5c31283fc903)

Inspecting the website storage we see some exotic cookies that seem to be related to some cryptographic algorithm:
![image](https://github.com/user-attachments/assets/723b3883-d894-434a-ba28-71f0b6e9b04d)
As the message that shows in the site seems to hint crypt, I assumed it was a reference to the crypt(C) encryption algorithm which works on 8 characters blocks in ECB mode (every block is encrypted indipendently at a time, and transforms them in a sequence of 13 characters with a salt of two characters from the alphabet "./0–9A-Za-z" and using invalid characters in the salt will cause crypt() to fail.

Making http request with different headers, it’s easy to see the cookie changing.
![image](https://github.com/user-attachments/assets/5a7b3d8d-3ebf-44c9-920b-c436d24f48d7)

If I try to change user=admin I get an error probably because that information is also encrypted at the start of the secure_cookie.
![image](https://github.com/user-attachments/assets/553bb25d-07ab-462c-8518-9b8b42006bd1)

Looking at the cookie we see that there is a repetition of ```C8``` so this is the salt, and indicates that the encryption works with blocks of 8 characters in ECB mode (every block is encrypted independently).
![image](https://github.com/user-attachments/assets/a84cd5c9-1510-4fc8-b108-71b265aa2d4a)

Since username is at the beginning of the encrypted string (we can see the User Agent below) we can simply do a plaintext attack considering the first block ```C80n81X03zNi6``` that is the encryption of ```guest:Mo``` and substituting it with the encryption of ```admin:Mo``` .
![image](https://github.com/user-attachments/assets/34e618c3-c36c-4b5f-ad34-26e8e37c0f8c)

Given this using the below php script to calculate the hash for the string ```admin:Mo"``` with a salt of ```C8``` we get the hash ```C8R2bOMZxrT.M```.
![image](https://github.com/user-attachments/assets/88255d4f-20a0-4ab7-b1d7-9d4960e5e554)

Replacing the first block of 8 characters of the cookie with this hash we obtain the first flag!
![image](https://github.com/user-attachments/assets/60545c80-4d0c-4376-a00b-94b73e1a0872)

## 2nd Flag
Since each chunk is 8 bytes drawn from a 65‑character alphabet there are 65^8 ≈ 282 trillion possibilities per chunk, that’s way too many to try brute force!   
Hopefully theres a trick we can use, if you can arrange that seven of the 8 bytes are already known, you only need to bruteforce 65 possibilities per byte, so recovering an arbitrarily long key becomes trivial, since we control the User‑Agent header completely, we can use it as a “padding beacon", and by sending a UA string of exactly the right length, we force the secret‑key chunk on the server side.
![image](https://github.com/user-attachments/assets/68cda676-d0f6-4997-b99e-03d15cdab443)

The php script (see the comment on the top of the README.md):
![image](https://github.com/user-attachments/assets/e5b2daa3-429a-4f82-872f-9042532ae2b7)

Script running:
![image](https://github.com/user-attachments/assets/7407509b-f39d-46fe-acf3-e456d7300c9a)

The 2nd flag:
![image](https://github.com/user-attachments/assets/0f5470fe-9d4b-4d96-94aa-dad0113e24f2)
