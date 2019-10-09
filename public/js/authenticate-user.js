$(function () {
    if ($$.getUser() === null) {
        $$.to('/');
    }
    if ($$.getToken() === null) {
        $$.to('/');
    }
});

