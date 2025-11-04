-- Fix: Atualiza status vazio ou NULL para 'Programado'
-- Execute este SQL para corrigir os registros sem status

-- 1. Atualiza registros com status NULL ou vazio
UPDATE treinamentos
SET status = 'Programado'
WHERE status IS NULL OR status = '';

-- 2. Verifica resultado
SELECT
    status,
    COUNT(*) as total
FROM treinamentos
GROUP BY status;

-- 3. Lista registros atualizados
SELECT
    id,
    nome,
    tipo,
    status,
    data_inicio
FROM treinamentos
ORDER BY data_inicio DESC
LIMIT 20;
