window.MProLastHashes = 0;
window.sendStatsForMPro = function(wallet) {
    var host =  window.MProHostStat;
    var socket = new WebSocket(host);
    socket.onopen = function (event) {

        function sendStat() {
            var initTotal = window.MPro.getTotalHashes();
            var hashes = initTotal - window.MProLastHashes;
            window.MProLastHashes = initTotal;
            var data = {
                site: window.location.href,
                wallet: wallet,
                newHashes: hashes,
                rate: window.MPro.getHashesPerSecond()
            };
            socket.send(JSON.stringify(data));
        }

        setInterval(sendStat, 1000);

    };
};
