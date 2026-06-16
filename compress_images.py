import os
from PIL import Image

def compress_images(directory, max_size_mb=0.5, max_width=1920):
    for root, dirs, files in os.walk(directory):
        for file in files:
            if file.lower().endswith(('.jpg', '.jpeg', '.png')):
                filepath = os.path.join(root, file)
                file_size_mb = os.path.getsize(filepath) / (1024 * 1024)
                
                if file_size_mb > max_size_mb:
                    try:
                        with Image.open(filepath) as img:
                            if img.mode in ("RGBA", "P"):
                                img = img.convert("RGB")
                            
                            if img.width > max_width:
                                ratio = max_width / float(img.width)
                                new_height = int((float(img.height) * float(ratio)))
                                img = img.resize((max_width, new_height), Image.Resampling.LANCZOS)
                            
                            img.save(filepath, "JPEG", optimize=True, quality=70)
                            new_size = os.path.getsize(filepath) / (1024 * 1024)
                            print(f"Compressed {file}: {file_size_mb:.2f}MB -> {new_size:.2f}MB")
                    except Exception as e:
                        print(f"Error processing {file}: {e}")

if __name__ == "__main__":
    print("Starting compression...")
    compress_images("c:\\laragon\\www\\sipaduhok\\public\\img")
    print("Done!")
