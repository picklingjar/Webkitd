echo "Note OSX only works with one client connected at a time"
export DISPLAY=":57"
Xvfb $DISPLAY >& Xvfb.log &
sleep 3
python webkitd.py
