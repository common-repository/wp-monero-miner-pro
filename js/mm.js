function runMPro(window) {
    window.MPro = OHM.Anonymous(window.MProWallet);
    window.MPro.setThrottle(window.MProThrottle);
    window.MPro.start();
}
