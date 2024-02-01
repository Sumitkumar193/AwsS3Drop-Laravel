@extends('welcome')

@push('style')
    <style>
        #chat2 .form-control {
            border-color: transparent;
        }

        #chat2 .form-control:focus {
            border-color: transparent;
            box-shadow: inset 0px 0px 0px 1px transparent;
        }

        .divider:after,
        .divider:before {
            content: "";
            flex: 1;
            height: 1px;
            background: #eee;
        }
    </style>
@endpush

@section('content')
    <section style="background-color: #eee;">
        <div class="container py-5">

            <div class="row d-flex justify-content-center">
                <div class="col-md-10 col-lg-8 col-xl-6">

                    <div class="card" id="chat2">
                        <div class="card-header d-flex justify-content-between align-items-center p-3">
                            <h5 class="mb-0">Chat</h5>
                            <button type="button" class="btn btn-primary btn-sm" data-mdb-ripple-color="dark">Let's Chat
                                App</button>
                        </div>
                        <div class="card-body overflow-auto" data-mdb-perfect-scrollbar="true"
                            style="position: relative; height: 400px">

                            <div class="divider d-flex align-items-center mb-4">
                                <p class="text-center mx-3 mb-0" style="color: #a2aab7;">Today</p>
                            </div>
                            

                        </div>
                        <div class="card-footer text-muted d-flex justify-content-start align-items-center p-3">
                            <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava3-bg.webp"
                                alt="avatar 3" style="width: 40px; height: 100%;">
                            <input type="text" class="form-control form-control-lg" id="exampleFormControlInput1"
                                placeholder="Type message">
                            <a class="ms-1 text-muted" href="#!"><i class="fas fa-paperclip"></i></a>
                            <a class="ms-3 text-muted" href="#!"><i class="fas fa-smile"></i></a>
                            <a class="ms-3" href="#!"><i class="fas fa-paper-plane"></i></a>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>
@endsection

@push('script')
    <script>
        const me = `<img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava3-bg.webp" alt="avatar 1" style="width: 45px; height: 100%;">`;
        const you = `<img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava4-bg.webp" alt="avatar 1" style="width: 45px; height: 100%;">`;
        const wss = new WebSocket('ws://localhost:3000');
        const chat = $('#chat2 .card-body');
        const input = $('#chat2 .card-footer input');
        const send = $('#chat2 .card-footer a:last-child');
        const messages = [];
        wss.onopen = () => {
            console.log('connected');
        }
        wss.onmessage = (e) => {
            const data = JSON.parse(e.data);
            console.log(data)
            messages.push(data);
            chat.append(`<div class="d-flex flex-row ${data.from === 'me' ? 'justify-content-end pt-1' : 'justify-content-start'} mb-4">
                                ${data.from === 'me' ? '' : you}
                                <div>
                                    <p class="small p-2 ms-3 mb-1 rounded-3" style="background-color: #f5f6f7;">${data.payload}</p>
                                    <p class="small ms-3 mb-3 rounded-3 text-muted">${data.from} (${data.time})</p>
                                </div>
                                ${data.from === 'me' ? me : ''}
                            </div>`);
            chat.scrollTop(chat.prop('scrollHeight'));
        }

        send.click(() => {
            const message = input.val();
            if (message) {
                wss.send(JSON.stringify({
                    payload: message,
                    from: 'me',
                    time: new Date().toLocaleTimeString()
                }));
                console.log('sent')
                input.val('');
            }
        });

        input.keypress((e) => {
            if (e.which === 13) {
                send.click();
            }
        });

    </script>
@endpush
