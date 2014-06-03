mathhub_modules=()
mathhub_modules+=("sites/all/modules/oaff")
mathhub_modules+=("sites/all/modules/mmt")
mathhub_modules+=("sites/all/modules/latexml")
mathhub_modules+=("sites/all/modules/jobad")


for module_folder in "${mathhub_modules[@]}" 
do
  for file in `find $module_folder | grep ".module$\|.install$\|.php$"`
  do
    echo "Checking File: '$file'";
    php5 $file &> /dev/null;
    ret_code=$?;
    if [ $ret_code -ne 0 ] 
      then 
      echo "File '$file' failed with exit code $ret_code and message: ";
      echo $(php5 $file)
      echo "Build Failed"
      exit 1
    fi
  done
done
echo "Build Succeeded"
exit 0

