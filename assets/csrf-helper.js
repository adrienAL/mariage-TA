// ============================================
// Utilitaire CSRF - Gestion des tokens
// ============================================

/**
 * Récupère un token CSRF depuis le serveur
 * @param {string} formName - Nom du formulaire
 * @returns {Promise<string>} Le token CSRF
 */
async function getCSRFToken(formName = 'default') {
  try {
    const res = await fetch('api/get_csrf_token.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({formName: formName})
    });
    
    if (!res.ok) {
      console.error('Erreur récupération token CSRF');
      return null;
    }
    
    const data = await res.json();
    return data.token;
  } catch (error) {
    console.error('Erreur CSRF:', error);
    return null;
  }
}

/**
 * Ajoute le token CSRF à un objet FormData
 * @param {FormData} formData - Les données du formulaire
 * @param {string} formName - Nom du formulaire
 */
async function addCSRFToFormData(formData, formName = 'default') {
  const token = await getCSRFToken(formName);
  if (token) {
    formData.append('csrf_token', token);
  }
  return formData;
}

/**
 * Ajoute le token CSRF à un objet JSON
 * @param {Object} data - Les données JSON
 * @param {string} formName - Nom du formulaire
 */
async function addCSRFToJSON(data, formName = 'default') {
  const token = await getCSRFToken(formName);
  if (token) {
    data.csrf_token = token;
  }
  return data;
}
