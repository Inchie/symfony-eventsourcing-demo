#!/bin/bash
if [ ! -d "$APACHE_RUN_DIR" ]; then
	mkdir "$APACHE_RUN_DIR"
	chown $APACHE_RUN_USER:$APACHE_RUN_GROUP "$APACHE_RUN_DIR"
fi
if [ -e "$APACHE_PID_FILE" ]; then
	echo "Apache PID File exists"	
	rm "$APACHE_PID_FILE"
fi

echo "$APACHE_PID_FILE"
/usr/sbin/apache2ctl -D FOREGROUND
