import dotenv from "dotenv";
import http from "http";
import express from "express";
import WebSocket from "./websocket.js";

dotenv.config();

const app = express();

const server = http.createServer(app);

WebSocket(server);

server.listen(process.env.WS_PORT, () => {
    console.log(`Server started on port ${process.env.WS_PORT}`);
});

console.log("wss up");
