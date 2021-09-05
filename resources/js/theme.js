const container = $('.theme-toggle');
const config = {brightness: 100};
const THEMES = {
    DARK: {name: 'Dark', ui: '🌒'},
    LIGHT: {name: 'Light', ui: '🌅'},
    _STORAGE_KEY: 'dr-theme',
    DEFAULT: function () {
        return THEMES.LIGHT;
    }
}
const themeMethods = {
    [THEMES.LIGHT.name]: function () {
        changeTheme(DarkReader.disable, THEMES.LIGHT);
    },
    [THEMES.DARK.name]: function () {
        changeTheme(DarkReader.enable, THEMES.DARK);
    },
}

function changeTheme(themeFn, themeName) {
    themeFn();
    saveTheme(themeName)
    container.html(nextThemeContent(themeName).ui);
}

$(window).ready(() => {
    let theme;
    if (hasSavedTheme()) {
        theme = loadTheme();
    } else {
        DarkReader.auto(config);
    }
    onThemeChange(theme);

    container.click(() => {
        onThemeChange();
    });
});

function onThemeChange(theme = undefined) {
    if (theme === undefined) {
        theme = DarkReader.isEnabled() ? THEMES.LIGHT : THEMES.DARK;
    }
    themeMethods[theme.name]();
}

function saveTheme(theme) {
    localStorage.setItem(THEMES._STORAGE_KEY, JSON.stringify(theme));
}

function loadTheme() {
    return JSON.parse(localStorage.getItem(THEMES._STORAGE_KEY) || THEMES.DEFAULT());
}

function hasSavedTheme() {
    return localStorage.getItem(THEMES._STORAGE_KEY) !== null;
}

function nextThemeContent(current) {
    return current === THEMES.DARK ? THEMES.LIGHT : THEMES.DARK;
}
