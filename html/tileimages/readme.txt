# switching to old-fashionned name to tilemap id :
copy wallu.gif   1.gif
copy walli.gif 101.gif
copy hallu.gif   2.gif
copy halli.gif 102.gif
copy stdnu.gif   3.gif
copy stdni.gif 103.gif
copy stupu.gif   4.gif
copy stupi.gif 104.gif
copy  pitu.gif   5.gif
copy  piti.gif 105.gif
copy ewdcu.gif   6.gif
copy ewdci.gif 106.gif
copy nsdcu.gif   7.gif
copy nsdci,gif 107.gif
copy ewdou.gif   8.gif
copy ewdoi,gif 108.gif
copy nsdou.gif   9.gif
copy nsdoi.gif 109.gif
copy  twcu.gif  10.gif
copy  twci.gif 110.gif
copy  twou.gif  11.gif
copy  twoi.gif 111.gif
copy  telu.gif  12.gif
copy  teli.gif 112.gif
copy  edge.gif   0.gif

# added 100.gif

# assembling all images into a sprite: (uses ImageMagick)
montage -verbose [0-9].gif 1[0-9].gif 10[0-9].gif 11[0-9].gif -geometry 16x16 -tile 13x2 tiles.png
identify -verbose tiles.png
