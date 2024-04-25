const express = require('express');
const swaggerUi = require('swagger-ui-express');
const swaggerDocument = require('./swagger');
const _ = require('lodash');

// Mock Resources
const MockStudentResponse = require('./resources/MockStudent.json');

const app = express();
const port = 3000;

app.get('/student', (req, res) => {
    const { nim } = req.query;
    return res.json(MockStudentResponse);
});

// Redirect from the root URL to '/api-docs'
app.get('/', (req, res) => {
    res.redirect('/api-docs');
});


// Serve the Swagger documents at '/api-docs'
app.use('/api-docs', swaggerUi.serve, swaggerUi.setup(swaggerDocument));

// Start the server
app.listen(port, () => {
    console.log(`Server running at http://localhost:${port}`);
});
