from pathlib import Path
import json, shutil, zipfile

root = Path(__file__).resolve().parents[1]
out = root / 'dist/downloads'
out.mkdir(parents=True, exist_ok=True)
for name in ('oakline', 'oakline-estimates'):
    source = root / 'wordpress' / name
    shutil.copytree(root / 'licenses', source / 'licenses', dirs_exist_ok=True)
    shutil.copy2(root / 'THIRD_PARTY_NOTICES.md', source / 'THIRD_PARTY_NOTICES.md')
    with zipfile.ZipFile(out / (name + '.zip'), 'w', zipfile.ZIP_DEFLATED) as z:
        for f in source.rglob('*'):
            if f.is_file():
                z.write(f, f.relative_to(source.parent))
base = 'https://mhashim-eng.github.io/hashim-portfolio/oakline'
blueprint = {'preferredVersions': {'php': '8.3', 'wp': 'latest'}, 'login': True, 'landingPage': '/', 'steps': [
    {'step': 'installTheme', 'themeData': {'resource': 'url', 'url': base + '/downloads/oakline.zip'}, 'options': {'activate': True}},
    {'step': 'installPlugin', 'pluginData': {'resource': 'url', 'url': base + '/downloads/oakline-estimates.zip'}, 'options': {'activate': True}},
    {'step': 'runPHP', 'code': "<?php require '/wordpress/wp-load.php'; wp_set_current_user(1); $r=oakline_seed(); if(is_wp_error($r)){throw new Exception($r->get_error_message());}"}
]}
(root / 'dist/blueprint.json').write_text(json.dumps(blueprint, indent=2))
print('Packaged WordPress theme, plugin and Playground blueprint.')
