import os
import re

dirs = ["admin", "bendahara", "guru", "ketua", "orang-tua", "sekretaris", "siswa", "waka", "wali-kelas"]
base_path = r"c:\laragon\www\sipaduhok\resources\views"

for d in dirs:
    print(f"\n=== ROLE: {d.upper()} ===")
    partials_path = os.path.join(base_path, d, "partials")
    if not os.path.exists(partials_path):
        continue
        
    for file in os.listdir(partials_path):
        if "sidebar" in file and file.endswith(".blade.php"):
            print(f"File: {file}")
            filepath = os.path.join(partials_path, file)
            with open(filepath, 'r', encoding='utf-8') as f:
                content = f.read()
                
                # Match menu items - look for '<div>Text</div>' inside <a> tags
                menus = re.findall(r'<a[^>]*class="menu-link[^>]*>.*?<div[^>]*>(.*?)</div>', content, re.DOTALL)
                for menu in menus:
                    clean_menu = menu.strip()
                    if clean_menu and '{' not in clean_menu: # Ignore raw blade tags if any
                        print(f"  - {clean_menu}")
