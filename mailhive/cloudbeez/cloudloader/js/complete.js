/*!
 * Install Progress (Step 5)
 */

Installer.Pages.installComplete.beforeUnload = function () {
    // Hide the leaves
//    $(document).octoberLeaves('stop')
}

Installer.Pages.installComplete.beforeShow = function () {
    return true;

//    var backendUri = Installer.Data.config.backend_uri,
//        baseUrl = installerBaseUrl
//
//    if (baseUrl.charAt(baseUrl.length - 1) == '/')
//        baseUrl = baseUrl.substr(0, baseUrl.length - 1)
//
//    Installer.Pages.installComplete.baseUrl = installerBaseUrl
//    Installer.Pages.installComplete.backendUrl = baseUrl + backendUri

}

Installer.Pages.installComplete.init = function () {
    // Purrty leaves
//    $(document).octoberLeaves({ numberOfLeaves: 10, cycleSpeed: 40 })
//    $(document).octoberLeaves('start')
}
