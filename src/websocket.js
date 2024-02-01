import { WebSocketServer } from 'ws';
import {Animals} from './animals.js';

export default function WebSocket(server) {
    const Clients = new Map();
    const wss = new WebSocketServer({ server });
    wss.on("connection", (ws, req) => {
        const uniqueId = uuidv4();

        Clients.set(uniqueId, ws);
        console.log(`Connected Client ${uniqueId}`);

        ws.on("close", () => {
            Clients.delete(uniqueId);
            console.log(`Disconnected Client ${uniqueId}`);
        });

        ws.on('message', function incoming(data) {
            const { type, payload, time } = JSON.parse(data);

            wss.clients.forEach(function each(client) {
                const message = {
                    "from" : uniqueId,
                    "type": type,
                    "payload": payload,
                    "time": time
                }
                if (client.readyState === 1) {
                    message.from = client !== ws ? uniqueId : "me";
                    client.send(JSON.stringify(message));
                }
            });
        });

        ws.on("error", (ws, error) => {
            console.log(`Error ${uniqueId} : ${error}`);
            ws.send(JSON.stringify({ error }));
        });
    });

    function uuidv4() {
        const randomAnimal = Animals[Math.floor(Math.random() * Animals.length)];
        return `Anonymous-${randomAnimal}-Zxxx-xxxxxxCxxxxx`.replace(/[x]/g, function (c) {
            var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }

}