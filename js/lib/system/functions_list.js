


// Functions list
function timeCalc(timestamp, format) { // Edit this one a bit. It works for now though (especially with .substr() being deprecated)
    /* AVAILABLE FORMATS:
    MM-DD-YYYY
    MM-DD-YYYY HH:MMa
    MM-DD-YY HH:MMa
    HH:MMa
    */
    const date = new Date(timestamp);
    const hours = date.getHours();
    const minutes = date.getMinutes();
    const ampm = hours >= 12 ? 'pm' : 'am';
  
    const padZero = (num) => (num < 10 ? '0' + num : num);
  
    const formattedDate = format
      .replace('MM', padZero(date.getMonth() + 1))
      .replace('DD', padZero(date.getDate()))
      .replace('YYYY', date.getFullYear())
      .replace('YY', date.getFullYear().toString().substr(-2))
      .replace('HH', padZero(hours % 12 || 12))
      .replace('MM', padZero(minutes))
      .replace('a', ampm.toLowerCase());
  
    return formattedDate;
}