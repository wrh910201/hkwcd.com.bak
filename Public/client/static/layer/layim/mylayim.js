var xxim = new Vue({
    el:"#xximmm",
    data: {
        user: {
            nickname: operator,
            avatar: avatar
        },
        ws: null,
        protocol: protocol,
        encrypt_data: encrypt_data,

        init_finish: false,
        show_chating: false,
        search_key: "",
        show_user_tab: true,
        show_group_tab: false,
        show_history_tab: false,
        online_user: [],
        chating: [],
        chating_box: {
            display: false,
            min: false,
            chating_index: 0,
            current_chating: {},
            other_style: {
                width: '621px',
                height: '500px',
                top: '30px',
                left: '375.5px',
                display: 'none',
            },
            editing_content: "",  //正在输入的内容
        },
        heart_timer: null, //心跳定时器
        timer_interval: 8000,
    },
    mounted: function() {
        var that = this;
        this.ws = new WebSocket(this.protocol);
        this.ws.onopen = function () {
            var data = {
                encrypt_data: that.encrypt_data,
                message_type: 'auth',
            };
            that.ws.send(JSON.stringify(data));
            that.init_finish = true;
        };
        this.ws.onmessage = function (e) {
            var data = JSON.parse(e.data);
            // console.log(data);
            switch(data['message_type']) {
                case "auth":
                    var data = {
                        "message_type": "online",
                    };
                    this.send(JSON.stringify(data));
                    that.registerTimer();
                    break;
                case "ping": break;
                case "init": break;
                case "online":
                    that.online_user = data.data;
                    break;
                case "login":
                    that.login(data.data);
                    break;
                case "logout":
                    that.logout(data.data);
                    break;
                case "message":
                    console.log(data);
                    that.receive(data);
                    break;
            }
//        alert("收到服务端的消息：" + e.data);
        };
        this.ws.onclose = function (e) {
            window.clearInterval(that.heart_timer);
        };
    },
    methods: {
        toggleChat: function() {
            this.show_chating = !this.show_chating;
        },
        showTab: function(index) {
            switch(index) {
                case 1:
                    this.show_user_tab = true;
                    this.show_group_tab = false;
                    this.show_history_tab = false;
                    break;
                case 2:
                    this.show_user_tab = false;
                    this.show_group_tab = true;
                    this.show_history_tab = false;
                    break;
                case 3:
                    this.show_user_tab = false;
                    this.show_group_tab = false;
                    this.show_history_tab = true;
                    break;
            }
        },
        registerTimer: function() {
            this.registerTimer = window.setInterval(this.ping, this.timer_interval);
        },
        expand: function(group_id) {
            for( var id in this.online_user ) {
                if( this.online_user[id].group_id == group_id ) {
                    this.online_user[id].expand = !this.online_user[id].expand;
                }
            }
        },
        ping: function() {
            var data = {"message_type": "ping"};
            this.ws.send(JSON.stringify(data));
        },
        show_chat_box: function(user) {
            //计算other_style
            this.chating_box.other_style.display = "block";


            this.chating_box.display = true;
            this.add_chating(user);
        },
        min_chat_box: function() {
            this.chating_box.other_style.display = "none";
            this.chating_box.min = true;
        },
        restore_chat_box: function() {
            this.chating_box.other_style.display = "block";
            this.chating_box.min = false;
        },
        close_chat_box: function() {
            this.chating_box.other_style.display = "none";
            this.chating_box.display = false;
        },
        add_chating: function(user) {
            var index = this.chating.indexOf(user);
            this.chating_box.current_chating = user;
            if( index == -1 ) {
                this.chating.push(user);
                this.chating_box.chating_index = this.chating.indexOf(user);
            } else {
                if( this.chating_box.min == true ) {
                    this.chating_box.min = false;
                }
                this.chating_box.chating_index = index;
            }
        },
        change_chating: function(user, index) {
            // console.log("change_chating");
            // console.log("当前聊天：" + user.name);
            this.chating_box.current_chating = user;
            this.chating_box.chating_index = index;
        },
        close_chating: function(user, index) {
            // console.log("close_chating");
            // console.log("传入的index = " + index);
            // console.log("当前的index = " + this.chating_box.chating_index);
            // console.log("要删除的聊天：" + user.name);

            this.chating.splice(index, 1);
            // console.log("长度：" + this.chating.length);
            if( this.chating_box.chating_index === index ) {
                for( var id in this.chating ) {
                    if( id == 0 ) {
                        this.chating_box.current_chating = this.chating[id];
                        this.chating_box.chating_index = 0;
                        break;
                    }
                }
            }
        },
        transmit: function() {
            var that = this;
            console.log("transmit");
            console.log(this.chating_box.editing_content);
            if (this.chating_box.editing_content.replace(/\s/g, '') === '') {
                layer.tips('说点啥呗！', '#layim_write', 2);
            } else {

                var data = {
                    message_type: "message",
                    to: this.chating_box.current_chating.id,
                    content: this.chating_box.editing_content,
                };
                this.ws.send(JSON.stringify(data));

                var message = {
                    time: this.getNowFormatDate(),
                    content: this.chating_box.editing_content,
                    face: this.user.avatar,
                    name: this.user.nickname,
                    type: 'me',
                };
                // if( typeof this.chating[this.chating_box.chating_index].message == 'object') {
                //     this.chating[this.chating_box.chating_index].message = [];
                // }
                this.chating[this.chating_box.chating_index].message.push(message);
                this.chating_box.current_chating = this.chating[this.chating_box.chating_index];
                this.chating_box.editing_content = "";
                setTimeout(function() {
                    var key = that.chating_box.current_chating.type + that.chating_box.current_chating.id;
                    var selector = "layim_area" + key;
                    var height = document.getElementById(selector).scrollHeight;
                    document.getElementById(selector).scrollTop = height;
                    // alert(height);
                },500);

                // imarea.scrollTop(height + 110);
                // this.chating[this.chating_box.chating_index].message.push(message);

            }
        },
        receive: function(data) {
            var that = this;
            var message = {
                time: data.time,
                content: data.content,
                face: data.face,
                name: data.name,
                type: '',
            };
            var exists = false;
            for( var id in this.chating ) {
                if( this.chating[id].id == data.from ) {
                    exists = true;
                    this.chating[id].message.push(message);
                    setTimeout(function() {
                        var key = that.chating[id].type + that.chating[id].id;
                        var selector = "layim_area" + key;
                        var height = document.getElementById(selector).scrollHeight;
                        document.getElementById(selector).scrollTop = height;
                    },500);
                    break;
                }
            }
            if( !exists ) {
                for( var i in this.online_user ) {
                    for( var j in this.online_user[i].items ) {
                        if( this.online_user[i].items[j].id == data.from ) {
                            var user = this.online_user[i].items[j];
                            user.message.push(message);
                            this.show_chat_box(user);
                            break;
                        }
                    }
                }
            }
        },
        logout: function(data) {
            console.log("logout");
            console.log(data);
            var that = this;
            for( var i in this.online_user) {
                if( this.online_user[i].group_id == data.group_id ) {
                    for( var j in this.online_user[i].items ) {
                        if( this.online_user[i].items[j].id == data.item.id ) {
                            this.online_user[i].items.splice(j, 1);
                        }
                    }
                    if( this.online_user[i].items.length == 0 ) {
                        this.online_user.splice(i, 1);
                    }
                }
            }
            for( var i in this.chating ) {
                if( this.chating[i].id == data.item.id ) {
                    this.close_chating(this.chating[i], i);
                    if( this.chating.length == 0 ) {
                        this.close_chat_box();
                    }
                }
            }
        },
        login: function(data) {
            console.log("login");
            console.log(data);
            var that = this;
            var group_exists = false;
            var user_exists = false;
            for( var i in this.online_user) {
                if( this.online_user[i].group_id == data.group_id ) {
                    group_exists = true;
                    for( var j in this.online_user[i].items ) {
                        if( this.online_user[i].items[j].id == data.item.id ) {
                            user_exists = true;
                            break;
                        }
                    }
                    if( !user_exists ) {
                        console.log(data.item.name + "不存在");
                        var item = {
                            id: data.item.id,
                            name: data.item.name,
                            face: data.item.face,
                            type: "one",
                            message: [],
                        };
                        this.online_user[i].items.push(item);
                    }
                    break;
                }
            }
            //分组不存在，必然用户不存在
            if (!group_exists) {
                var group = {
                    group_id: data.group_id,
                    group_name: data.group_name,
                    expand: false,
                    nums: 0,
                    items: [
                        {
                            id: data.item.id,
                            name: data.item.name,
                            face: data.item.face,
                            type: "one",
                            message: [],
                        }
                    ],
                };
                this.online_user.push(group);
            }
        },
        getNowFormatDate:function() {
            var date = new Date();
            var seperator1 = "-";
            var seperator2 = ":";
            var month = date.getMonth() + 1;
            var strDate = date.getDate();
            if (month >= 1 && month <= 9) {
                month = "0" + month;
            }
            if (strDate >= 0 && strDate <= 9) {
                strDate = "0" + strDate;
            }
            var currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate
                + " " + date.getHours() + seperator2 + date.getMinutes()
                + seperator2 + date.getSeconds();
            return currentdate;
        },
    },
});