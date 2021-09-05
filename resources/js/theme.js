const container = $('.theme-toggle');
const config = {brightness: 100};
const THEMES = {
    DARK: 'Dark',
    LIGHT: 'Light',
    _STORAGE_KEY: 'dr-theme',
    DEFAULT: function () {
        return THEMES.LIGHT;
    }
}
const themeMethods = {
    [THEMES.LIGHT]: function () {
        changeTheme(DarkReader.disable, THEMES.LIGHT);
    },
    [THEMES.DARK]: function () {
        changeTheme(DarkReader.enable, THEMES.DARK);
    },
}

function changeTheme(themeFn, themeName) {
    themeFn();
    saveTheme(themeName)
    container.html(nextThemeName(themeName));
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
    themeMethods[theme]();
}

function saveTheme(theme) {
    localStorage.setItem(THEMES._STORAGE_KEY, theme);
}

function loadTheme() {
    return localStorage.getItem(THEMES._STORAGE_KEY) || THEMES.DEFAULT();
}

function hasSavedTheme() {
    return localStorage.getItem(THEMES._STORAGE_KEY) !== null;
}

function nextThemeName(current) {
    return current === THEMES.DARK ? THEMES.LIGHT : THEMES.DARK;
}
