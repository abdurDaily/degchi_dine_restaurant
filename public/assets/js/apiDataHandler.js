

/**
 * Handles the response from an API request.
 *
 * @param {Object} response - The response from the API request.
 * @param {string} url - The URL that was requested.
 *
 * @returns {Object} The response data.
 */
function handleApiResponse(response, url) {
    const responseData = response.data;

    if (response.status < 200 || response.status >= 300) {
        console.error(`HTTP request to ${url} returned status code ${response.status}`);
    }

    return responseData;
}


/**
 * Makes a GET request to the given URL and returns the response data.
 *
 * @param {string} url - The URL to request.
 * @param {Object} [headers] - The headers to send with the request.
 * @param {Object} [params] - The query parameters to send with the request.
 *
 * @returns {Object} The response data.
 */
async function getApiData(url, headers = {}, params = {}) {

    try {
        const response = await axios.get(url, {
            headers: headers,
            params: params,
        });

        return handleApiResponse(response, url); // Assuming handleApiResponse is defined
    } catch (error) {
        if (axios.isAxiosError(error)) {
            console.error(`AxiosError: ${error.message}`);
        } else {
            console.error(`Error: ${error.message}`);
        }
        return null;
    }
}


/**
 * Makes a POST request to the given URL with the given data and returns the response data.
 *
 * @param {string} url - The URL to request.
 * @param {Object} data - The data to send with the request.
 * @param {Object} [headers] - The headers to send with the request.
 * @param {Object} [params] - The query parameters to send with the request.
 *
 * @returns {Object} The response data.
 */
async function postApiData(url, data, headers = {}, params = {}) {
    try {
        const response = await axios.post(url, data, {
            headers: headers,
            params: params,
        });

        return handleApiResponse(response.data, url); // Implement your own handleApiResponse function
    } catch (error) {
        console.error("Axios error: ", error.message);
        return null;
    }
}

/**
 * Makes a POST request to the given URL with the given data and files and returns the response data.
 *
 * @param {string} url - The URL to request.
 * @param {Object} data - The data to send with the request.
 * @param {Object} files - An object with property names as the keys and File objects or arrays of File objects as the values.
 * @param {Object} [headers] - The headers to send with the request.
 * @param {Object} [params] - The query parameters to send with the request.
 *
 * @returns {Object} The response data.
 */
async function postApiDataWithFiles(url, data, files, headers = {}, params = {}) {
    try {
        const formData = new FormData();

        // Append data
        for (const [key, value] of Object.entries(data)) {
            formData.append(key, value);
        }

        // Attach files
        for (const [key, file] of Object.entries(files)) {
            if (file instanceof File) {
                formData.append(key, file); // Single file
            } else if (Array.isArray(file)) {
                file.forEach((subfile, index) => {
                    formData.append(`${key}[${index}]`, subfile); // Multiple files
                });
            }
        }

        // Make the POST request
        const response = await axios.post(url, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
                ...headers
            },
            params: params,
        });

        return handleApiResponse(response.data, url); // Implement your own handleApiResponse function
    } catch (error) {
        console.error("Axios error: ", error.message);
        return null;
    }
}


/**
 * Makes a PUT request to the given URL with the provided data and returns the response data.
 *
 * @param {string} url - The URL to request.
 * @param {Object} data - The data to send with the request.
 * @param {Object} [headers] - The headers to send with the request.
 * @param {Object} [params] - The query parameters to send with the request.
 *
 * @returns {Object} The response data.
 */
async function putApiData(url, data, headers = {}, params = {}) {
    try {
        const response = await axios.put(url, data, {
            headers: headers,
            params: params,
        });

        return handleApiResponse(response.data, url); // Implement your own handleApiResponse function
    } catch (error) {
        console.error("Axios error: ", error.message);
        return null;
    }
}

/**
 * Makes a DELETE request to the given URL and returns the response data.
 *
 * @param {string} url - The URL to request.
 * @param {Object} [headers] - The headers to send with the request.
 * @param {Object} [params] - The query parameters to send with the request.
 *
 * @returns {Object} The response data.
 */
async function deleteApiData(url, headers = {}, params = {}) {
    try {
        const response = await axios.delete(url, {
            headers: headers,
            params: params,
        });

        return handleApiResponse(response.data, url); // Implement your own handleApiResponse function
    } catch (error) {
        console.error("Axios error: ", error.message);
        return null;
    }
}





// Call function example

// getApiData(url, null, params)
// .then(response => {
//     console.log(response.units.data); // Logs the API data
//     // set data as unit
//     var unit = response.units.data;
//     var unitOptions = '<option value="" disabled selected>Select unit</option>'; // Add disabled option
//     unitOptions += unit.map(function(item) {
//         return '<option value="' + item.id + '">' + item.unit_name + '</option>';
//     });
//     $('#unit').html(unitOptions);
// })
// .catch(error => {
//     console.error("Error fetching data:", error);
//     Command: toastr["error"]("Error fetching data:", error);

// })
