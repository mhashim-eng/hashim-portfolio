from pathlib import Path
import urllib.request,concurrent.futures,re
from PIL import Image
from io import BytesIO
r=Path(__file__).resolve().parents[1]
assets={'hero':'https://images.unsplash.com/photo-1643684391140-c5056cfd3436?w=1400&q=85&fit=crop','facial':'https://images.unsplash.com/photo-1552693673-1bf958298935?w=1000&q=80&fit=crop','studio':'https://images.unsplash.com/photo-1772378452022-94ee7971fe80?w=1200&q=80&fit=crop'}
def get(a):
 name,url=a;b=urllib.request.urlopen(url,timeout=40).read();im=Image.open(BytesIO(b));im.thumbnail((1400,1600));im.convert('RGB').save(r/'dist/assets'/f'{name}.webp','WEBP',quality=82);return name,im.size
print(list(concurrent.futures.ThreadPoolExecutor().map(get,assets.items())))
css=urllib.request.urlopen('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600&display=swap',timeout=30).read().decode();url=re.findall(r'url\(([^)]+)\)',css)[0];(r/'dist/assets/playfair.ttf').write_bytes(urllib.request.urlopen(url).read())
(r/'licenses/Playfair-Display-OFL.txt').write_bytes(urllib.request.urlopen('https://raw.githubusercontent.com/google/fonts/main/ofl/playfairdisplay/OFL.txt').read())
