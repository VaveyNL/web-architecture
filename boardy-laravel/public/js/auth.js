import { generateVerifier, generateChallenge, generateState } from './pkce.js';

const CLIENT_ID = '019e6aa9-3fbc-711e-9509-325567a027ba';
const REDIRECT_URI = window.location.origin + '/oauth/callback';
const API_BASE = '';   // пусто = тот же домен, запросы пойдут через Nginx

// --- Шаг 1: старт OAuth-flow ---
export async function startLogin() {
    const verifier = generateVerifier();
    const challenge = await generateChallenge(verifier);
    const state = generateState();

    sessionStorage.setItem('pkce_verifier', verifier);
    sessionStorage.setItem('oauth_state', state);
    sessionStorage.setItem('return_to', window.location.pathname);

    const params = new URLSearchParams({
        client_id: CLIENT_ID,
        response_type: 'code',
        redirect_uri: REDIRECT_URI,
        code_challenge: challenge,
        code_challenge_method: 'S256',
        state: state,
        scope: '*',
    });
    window.location = '/oauth/authorize?' + params;
}

// --- Шаг 2: обработка callback ---
export async function handleCallback() {
    const params = new URLSearchParams(window.location.search);
    const code = params.get('code');
    const state = params.get('state');
    if (!code) return;

    const savedState = sessionStorage.getItem('oauth_state');
    if (state !== savedState) {
        throw new Error('Invalid state - возможна CSRF-атака');
    }
    const verifier = sessionStorage.getItem('pkce_verifier');
    if (!verifier) throw new Error('Нет verifier в sessionStorage');

    const body = new URLSearchParams({
        grant_type: 'authorization_code',
        client_id: CLIENT_ID,
        code: code,
        code_verifier: verifier,
        redirect_uri: REDIRECT_URI,
    });
    const res = await fetch('/oauth/token', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        credentials: 'include',
        body: body,
    });
    const data = await res.json();

    sessionStorage.removeItem('pkce_verifier');
    sessionStorage.removeItem('oauth_state');

    if (data.access_token) {
        sessionStorage.setItem('access_token', data.access_token);
    }
    const returnTo = sessionStorage.getItem('return_to') || '/posts';
    sessionStorage.removeItem('return_to');
    window.location = returnTo;
}

// --- Шаг 3: silent refresh ---
export async function refreshToken() {
    const res = await fetch('/auth/refresh', {
        method: 'POST',
        credentials: 'include',
    });
    if (!res.ok) {
        startLogin();
        return null;
    }
    const data = await res.json();
    if (data.access_token) {
        sessionStorage.setItem('access_token', data.access_token);
        return data.access_token;
    }
    return null;
}

export function getToken() {
    return sessionStorage.getItem('access_token');
}
