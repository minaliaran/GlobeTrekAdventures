<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? "GlobeTrek Adventures"; ?></title>
    
    
    <link rel="icon" type="image/png" href="<?php echo appUrl('images/favicon.png'); ?>">
    
    
    <link rel="stylesheet" href="<?php echo appUrl('css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo appUrl('css/layout.css'); ?>">
    <link rel="stylesheet" href="<?php echo appUrl('css/components.css'); ?>">
    <link rel="stylesheet" href="<?php echo appUrl('css/pages.css'); ?>">
    <link rel="stylesheet" href="<?php echo appUrl('css/navigation.css'); ?>">
    <link rel="stylesheet" href="<?php echo appUrl('css/hotels.css'); ?>">
    <link rel="stylesheet" href="<?php echo appUrl('css/responsive.css'); ?>">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="<?php echo $bodyClass ?? ''; ?>">
    <div id="scroll-progress"></div>
