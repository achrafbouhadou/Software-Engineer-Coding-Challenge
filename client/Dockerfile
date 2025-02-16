# Use a Node image 
FROM node:20-alpine

# Set working directory inside the container
WORKDIR /app

# Copy the rest of the application files
COPY . .



EXPOSE 5173

# Start the development server
CMD ["npm", "run", "dev"]
