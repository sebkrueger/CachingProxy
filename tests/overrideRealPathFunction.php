<?php
/*------------------------------------------------------------------------------

   Project  : CachingProxy
   Filename : tests/overrideRealpathFunction.php
   Autor    : (c) Sebastian Krüger <krueger@secra.de>
   Date     : 19.03.2014

   For the full copyright and license information, please view the LICENSE
   file that was distributed with this source code.

   Description: overrride the realpath() function in namespace to do some tests

  ----------------------------------------------------------------------------*/

namespace secra\CachingProxy;

/**
 * Override realpath() in current namespace for testing
 *
 * @param string $path     the file path
 *
 * @return string
 */
function realpath($path)
{
    return $path;
}