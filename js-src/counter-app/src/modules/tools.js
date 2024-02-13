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
  // console.log(typeof places);
  places = "number" === typeof places ? places : 2;
  grouping = "string" === typeof grouping ? grouping : false;
  return (no * 1).toLocaleString(undefined, {
    minimumFractionDigits: places,
    maximumFractionDigits: places,
    useGrouping: grouping,
  });
};

export const getCalendarWeek = (date) => {
  date.setUTCHours(0, 0, 0, 0);
  // Thursday in current week decides the year.
  date.setUTCDate(date.getUTCDate() + 3 - ((date.getUTCDay() + 6) % 7));
  // January 4 is always in week 1.
  let week1 = new Date(date.getUTCFullYear(), 0, 4);
  // Adjust to Thursday in week 1 and count number of weeks from date to week1.
  return (
    1 +
    Math.round(
      ((date.getTime() - week1.getTime()) / 86400000 -
        3 +
        ((week1.getUTCDay() + 6) % 7)) /
        7
    )
  );
};

export const createDateObject = (date) => {
  const date_regex = /(\d{4})-(\d{2})-(\d{2})/;
  const date_matches = date.match(date_regex);

  console.log(date, date_matches);
  let currentDate = new Date(date_matches[1], date_matches[2]-1, date_matches[3],12);
  // alert(currentDate.toISOString());
  return currentDate;
};
