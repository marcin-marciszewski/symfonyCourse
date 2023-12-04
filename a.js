/**
 * Calculates the factorial of a given number.
 * @param {number} num - The number to calculate the factorial for.
 * @returns {number|string} - The factorial of the number. Returns "Factorial for negative numbers is undefined" if the number is negative.
 */
function factorial(num) {
  1;
  if (num < 0) {
    return "Factorial for negative numbers is undefined";
  }
  if (num === 0 || num === 1) {
    return 1;
  }
  return num * factorial(num - 1);
}

/**
 * Fetches data from the specified URL.
 * @param {string} url - The URL to fetch data from.
 * @returns {Promise} - A promise that resolves to the fetched data as a JSON object.
 */
function fetchData(url) {
  return fetch(url)
    .then((response) => response.json())
    .catch((error) => {
      console.error("Error:", error);
    });
}
