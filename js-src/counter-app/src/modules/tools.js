export const formatDate = (theDate, format) => {
  const dateArray = theDate.match(
    /(\d{4}).(\d{2}).(\d{2})\s(\d{2}).(\d{2})(?:.(\d{2})){0,1}/
  );

  format = format.replace("d", dateArray[3]);
  format = format.replace("m", dateArray[2]);
  format = format.replace("h", dateArray[4]);
  format = format.replace("i", dateArray[5]);
  format = format.replace("s", dateArray[6]);
  format = format.replace("Y", dateArray[1]);
  format = format.replace("y", dateArray[1].slice(2));

  return format;
};

export const setDecimalPlaces = (no, places, grouping) => {
  console.log(typeof places)
  places = "number" === typeof places ? places : 2;
  grouping = "string" === typeof grouping ? grouping : false;
  return (no * 1).toLocaleString(undefined, {
    minimumFractionDigits: places,
    maximumFractionDigits: places,
    useGrouping: grouping,
  });
};
