<?php
/**
 * 检查访问的url是否有权限访问
 * @param string $currentPath 访问的url路径
 * @return bool
 */
function auth(string $currentPath): bool
{
    if (true === getSession('isSuperAdmin')) {
        return true;
    }
    $authRuleUrlData = getSession('authRuleUrlData');
    if (!empty($authRuleUrlData) && in_array($currentPath, $authRuleUrlData, true)) {
        return true;
    }
    return false;
}