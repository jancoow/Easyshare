#/bin/bash

clip=$(xclip -sel clip -o)
url="web-url of server here"

regex='(https?|ftp|file)://[-A-Za-z0-9\+&@#/%?=~_|!:,.;]*[-A-Za-z0-9\+&@#/%=~_|]' 
if [[ $clip =~ $regex ]]
then
  curl -d url="$clip" -d plain="" $url | xclip -selection clipboard
else
    echo "Not a link, file maybe?"
    if [ -f ${cip} ]; then
       	 curl -F file=@"$clip" -F plain="" $url | xclip -selection clipboard
    else
	echo "Nope, not a url and file :("
    fi
fi
