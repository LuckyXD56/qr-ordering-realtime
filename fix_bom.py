
import os

def remove_bom(path):
    if not os.path.exists(path): return
    with open(path, "rb") as f:
        content = f.read()
    if content.startswith(b"\xef\xbb\xbf"):
        content = content[3:]
        with open(path, "wb") as f:
            f.write(content)
        print(f"Removed BOM from {path}")

# Fix RoleMiddleware
remove_bom("app/Http/Middleware/RoleMiddleware.php")
# Might as well check bootstrap/app.php since I used Set-Content there too!
remove_bom("bootstrap/app.php")

