export const formatDate = (theDate, format) => {
  const dateArray = theDate.match(/(\d{4}).(\d{2}).(\d{2})\s(\d{2}).(\d{2}).(\d{2})/);

  format = format.replace('d', dateArray[3])
  format = format.replace('m', dateArray[2])
  format = format.replace('Y', dateArray[1])
  format = format.replace('y', dateArray[1].slice(2));

  return format;
}