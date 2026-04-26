import re

filepath = r'c:\laragon\www\sipaduhok\resources\views\admin\partials\sneat-sidebar-menu.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# Try to extract headers and menu items sequentially
lines = content.split('\n')
current_header = 'General'

print("=== MENU ADMIN ===")
for line in lines:
    header_match = re.search(r'<span class="menu-header-text">(.*?)</span>', line)
    if header_match:
        current_header = header_match.group(1).strip()
        print(f"\n[ {current_header} ]")
        continue
    
    menu_match = re.search(r'<div.*?>(.*?)</div>', line)
    if menu_match and 'class="menu-link"' not in line: # Usually the div is inside the a tag
        # Double check it's part of a menu item
        menu_name = menu_match.group(1).strip()
        if menu_name and not menu_name.startswith('{') and not menu_name.startswith('@'):
            print(f"  - {menu_name}")
