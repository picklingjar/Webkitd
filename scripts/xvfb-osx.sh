echo "OS X is broke atm"
export DISPLAY=":57"
Xvfb $DISPLAY >& Xvfb.log &
sleep 3
python webkitd.py
