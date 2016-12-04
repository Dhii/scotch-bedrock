<?php
# -*- coding: utf-8 -*-

$projectDocroot    = getenv('DSBR_PROJECT_DOCROOT');
$siteTitle         = getenv('DSBR_SITE_TITLE');
$siteUrl           = getenv('DSBR_SITE_URL');
$siteAdminUser     = getenv('DSBR_SITE_ADMIN_USER');
$siteAdminPassword = getenv('DSBR_SITE_ADMIN_PASSWORD');
$siteAdminEmail    = getenv('DSBR_SITE_ADMIN_EMAIL');

/**
 * The variable $api points to an instance of WpProvisioner that gives
 * you access to the public API
 *
 * @var \WpProvision\Api\WpProvisioner $api
 */

// set the WordPess install directory. This is important for WP-CLI to run properly
$api->setWpDir($projectDocroot);

// add a provision routine named '0.1.0'. The VersionList contains all provision routines for all versions
$api->versionList()->addProvision('0.1.0',
    /**
     * The command provider gives you access to all Wp Commands like core, site, user or plugin
     *
     * @param \WpProvision\Api\WpCommandProvider $provider
     */
    function($provider) use (
        $siteUrl, $siteTitle, $siteAdminEmail, $siteAdminUser, $siteAdminPassword
    ) {

        $provider->core()->install(
            $siteUrl,
            [
                'login'         => $siteAdminUser,
                'email'         => $siteAdminEmail,
                'password'      => $siteAdminPassword
            ],
            [
                'title'         => $siteTitle
            ]
        );

        $provider->plugin()->activate([
        ]);
    }
);
