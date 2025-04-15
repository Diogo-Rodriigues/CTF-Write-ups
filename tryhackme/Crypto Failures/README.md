# Crypto Failures
> This CTF can be done in two ways, the 1st is without looking at the source code and the 2nd is looking at the source code, I will do both

## About the Challenge
Implementing your own military-grade encryption is usually not the best idea.

## 1st approach (without the source code)
As always I started by running an nmap scan on the target IP address.
![image](https://github.com/user-attachments/assets/c31eafd6-6141-48e5-b184-dfe6169d8321)
This gives me the information that there are 2 open ports, 22 (SSH) and 80 (HTTP), which tells me that a website is running on the target.

Entering the website that is running we come across this page that gives us a message that seems to refer to encryption:
![image](https://github.com/user-attachments/assets/d1e87654-d9e2-454e-bed0-5c31283fc903)

Inspecting the website storage we see some exotic cookies that seem to be related to some cryptographic algorithm:
![image](https://github.com/user-attachments/assets/723b3883-d894-434a-ba28-71f0b6e9b04d)
As the message the site seems to hint crypt, I assumed it was a reference to the crypt(C) encryption algorithm which works on 8 characters blocks in ECB mode (every block is encrypted indipendently at a time, and transforms them in a sequence of 13 characters with a salt of two characters from the alphabet "./0–9A-Za-z" and using invalid characters in the salt will cause crypt() to fail.

Making some http request with different headers, it’s easy to see that the cookie and the printed message are related to the User-Agent: because using User-Agent: A we get a sensibly shorter cookie.
![image](https://github.com/user-attachments/assets/5a7b3d8d-3ebf-44c9-920b-c436d24f48d7)



If I try to change user=admin I get an error probably because that information is also encrypted at the start of the secure_cookie.
### 
```python
import os
from banner import monkey


```

We only need to obtain the flag using the git command

## How to Solve?
because
