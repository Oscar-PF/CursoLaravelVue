const axios = require('axios');

axios
  .get('http://localhost:8000/api/resumes/6', {
    headers: {
      Authorization: 'Bearer 5|OU5Neq8IdYdvJjwMCSkZJ3LAwvOiFLgBqB19LmZ0',
    },
  })
  .then((res) => {
    console.log(res.data);
  })
  .catch((e) => {
    console.log(e.response.status);
    console.log(e.response.data);
  });
