argusfile=$1
#Run Table Data####
capstartdate=$(/bin/sed q $argusfile | awk -F "," '{print $2}')
capstarttime=$(/bin/sed q $argusfile | awk -F "," '{print $3}')
echo "HERE IS THE CAPSTARTDATE = $capstartdate"
echo "HERE IS THE CAPSTARTTIME = $capstarttime"
capenddate=$(/bin/sed '$!d' $argusfile | awk -F "," '{print $2}')
capendtime=$(/bin/sed '$!d' $argusfile | awk -F "," '{print $3}')
echo "HERE IS THE CAPENDDATE = $capenddate"
echo "HERE IS THE CAPENDTIME = $capendtime"
capstartprocessdate=$(date +'%Y/%m/%d')
capstartprocesstime=$(date +'%H:%M:%S')
echo "HERE IS THE Capture Processing Date = $capstartprocessdate"
echo "HERE IS THE Capture Processing Time = $capstartprocesstime"
capendprocessdate=$(date +'%Y/%m/%d')
capendprocesstime=$(date +'%H:%M:%S')
echo "HERE IS THE Capture Processing End Date = $capendprocessdate"
echo "HERE IS THE Capture Processing End Time = $capendprocesstime"


