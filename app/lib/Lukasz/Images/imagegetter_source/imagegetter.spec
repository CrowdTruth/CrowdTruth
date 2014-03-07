# -*- mode: python -*-
import os
a = Analysis(['imagegetter.py'],
             pathex=[os.getcwd()],
             hiddenimports=None,
             hookspath=None,
             runtime_hooks=None)
 

pyz = PYZ(a.pure)
exe = EXE(pyz,
          a.scripts,
          exclude_binaries=True,
          name='imagegetter.exe',
          debug=False,
          strip=None,
          upx=True,
          console=True )
coll = COLLECT(exe,
               a.binaries,
               a.zipfiles,
               a.datas,
               strip=None,
               upx=True,
               name='imagegetter')
