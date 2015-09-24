#/bin/bash
#Author:		Janco Kock
#Description:	A linux/php script to easy share media files, url's, documents or code snippets. 
#Dependencies: 	curl and xclip


clip=$(xclip -sel clip -o)
url="url of remote server here"

regex='(https?|ftp|file)://[-A-Za-z0-9\+&@#/%?=~_|!:,.;]*[-A-Za-z0-9\+&@#/%=~_|]' 
if [[ $clip =~ $regex ]] && [[ ! $clip =~ "\n" ]]
then
  curl -d url="$clip" -d plain="" $url | xclip -selection clipboard
else
    echo "Not a link, file maybe?"
    if [ -f "${clip}" ]; then
       	 curl -F file=@"$clip" -F plain="" $url | xclip -selection clipboard
    else
		echo "Nope, not a url and file :(. I will post it as text"
        curl -d code="${clip}" -d plain="" $url | xclip -selection clipboard
    fi
fi
