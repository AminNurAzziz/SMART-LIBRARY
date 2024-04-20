const express = require('express');
const swaggerUi = require('swagger-ui-express');
const swaggerDocument = require('./swagger');

const app = express();
const port = 3000;

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
