#/bin/bash
#Author:		Janco Kock
#Description:	A linux/php script to easy share media files, url's, documents or code snippets. 
#Dependencies: 	curl and xclip


clip=$(xclip -sel clip -o)
url="https://site.com/uploader.php?API_KEY=foo&plain=true"

# Check if it is a nautilus copy command
if [[ $clip =~ "x-special/nautilus-clipboard" ]]; then
	echo "Nautilus copy"
	clip=$(xclip -sel clip -o | tail -n +3)
	clip=${clip:7}
fi

regex='(https?|ftp|file)://[-A-Za-z0-9\+&@#/%?=~_|!:,.;]*[-A-Za-z0-9\+&@#/%=~_|]' 
if [[ $clip =~ $regex ]] && [[ ! $clip =~ "\n" ]]
then
  echo "Post as URL"
  curl -d url="$clip" -d plain="" $url | xclip -selection clipboard
else
    if [ -f "${clip}" ]; then
         echo "Post as file"
       	 curl -F file=@"$clip" -F plain="" $url | xclip -selection clipboard
    else
        echo "Post as snippet"
        curl -d snippet="${clip}" -d plain="" $url | xclip -selection clipboard
    fi
fi

