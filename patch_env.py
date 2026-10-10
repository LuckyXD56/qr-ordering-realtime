import re
with open(".env", "r", encoding="utf-8") as f:
    env = f.read()

env = re.sub(r"APP_URL=.*", "APP_URL=http://172.20.10.3:8000", env)
env = re.sub(r"REVERB_HOST=.*", 'REVERB_HOST="172.20.10.3"', env)
env = re.sub(r"VITE_REVERB_HOST=.*", 'VITE_REVERB_HOST="172.20.10.3"', env)

with open(".env", "w", encoding="utf-8") as f:
    f.write(env)
