# -*- mode: python -*-
import os
a = Analysis(['predict.py'],
             pathex=['os.getcwd()'],
             hiddenimports=None,
             hookspath=None,
             runtime_hooks=None)
             
##### include mydir in distribution #######
def extra_datas(mydir):
    def rec_glob(p, files):
        import os
        import glob
        for d in glob.glob(p):
            if os.path.isfile(d):
                files.append(d)
            rec_glob("%s/*" % d, files)
    files = []
    rec_glob("%s/*" % mydir, files)
    extra_datas = []
    for f in files:
        extra_datas.append((f.replace(os.getcwd() + '\\data\\', ''),  f, 'DATA'))

    return extra_datas
###########################################

# append the 'data' dir
a.datas += extra_datas(os.getcwd() + '\\data\\')


pyz = PYZ(a.pure)
exe = EXE(pyz,
          a.scripts,
          exclude_binaries=True,
          name='predict.exe',
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
               name='predict')
