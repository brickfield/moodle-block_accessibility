#!/bin/sh

#Arg 1 = text input file, Arg 2 = filename for output

# temp dir for storing chunks - no trailing slash
local tempdir='/var/tmp'
# Moodle's dirroot, needs to match $CFG->dirroot in config.php
local dirroot='/home/mark/moodle-dev/moodle2'


echo "Starting..."

start_time=$(date +%s)

#Make the scratch directory if its not there yet.
if [ ! -d "/tmp/sb_scratch" ];
	then
		echo "Temporary dir doesn't exist, creating."
    	mkdir /tmp/sb_scratch
fi

#if [ $lang == "" ]; 
#	then
#		lang="awb"
#fi

x=0


while read fileline
do
	#For each line, process audio, appending -n for each line.

	echo "Loading line into t2w..."
	#echo "$fileline" | /usr/bin/text2wave -scale 2 -o $tempdir/TTS-${2}-${x}.wav -eval "(voice_nitech_us_$3_arctic_hts)"
	echo "$fileline" | /usr/bin/text2wave -scale 2 -o $tempdir/TTS-${2}-${x}.wav

	#cpulimit -P /usr/bin/text2wave -l 30

	interim_time=$(date +%s)

	echo "Converting to MP3, time taken so far: $((interim_time - start_time))s"
	result=`lame -f -S $tempdir/TTS-${2}-${x}.wav $tempdir/TTS-${2}-${x}.mp3.tmp`

	#Move the temp file to final file.
	mv $tempdir/TTS-${2}-${x}.mp3.tmp $dirroot/blocks/accessibility/toolbar/server/TTS/cache/TTS-${2}-${x}.mp3

	#Remove the temporary scratch file.
	rm $tempdir/TTS-${2}-${x}.wav

	#echo $fileline
	x=`expr $x + 1`
done < $1

if [ $x = 0 ]
then
	echo "Loading single line into t2w..."
	#cat $1 | /usr/bin/text2wave -scale 2 -o $tempdir/TTS-${2}-0.wav -eval "(voice_nitech_us_$3_arctic_hts)"
	cat $1 | /usr/bin/text2wave -scale 2 -o $tempdir/TTS-${2}-0.wav
	echo "Converting to MP3"
	result=`lame -f -S $tempdir/TTS-${2}-0.wav $tempdir/TTS-${2}-0.mp3.tmp`

	mv $tempdir/TTS-${2}-0.mp3.tmp $dirroot/blocks/accessibility/toolbar/server/TTS/cache/TTS-${2}-0.mp3
	rm $tempdir/TTS-${2}-0.wav
fi;

finish_time=$(date +%s)

echo "Done. Exec Time: $((finish_time - start_time))s Chunks: $x"
