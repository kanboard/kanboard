<?php
#
# Markdown Extra  -  A text-to-HTML conversion tool for web writers
#
# PHP Markdown Extra
# Copyright (c) 2004-2013 Michel Fortin  
# <http://michelf.com/projects/php-markdown/>
#
# Original Markdown
# Copyright (c) 2004-2006 John Gruber  
# <http://daringfireball.net/projects/markdown/>
#
# Redistribution and use in source and binary forms, with or without
# modification, are permitted provided that the following conditions
# are met:
#
#     Redistributions of source code must retain the above copyright
#     notice, this list of conditions and the following disclaimer.
#
#     Redistributions in binary form must reproduce the above
#     copyright notice, this list of conditions and the following
#     disclaimer in the documentation and/or other materials provided
#     with the distribution.
#
#     Neither the name "Markdown" nor the names of its contributors
#     may be used to endorse or promote products derived from this
#     software without specific prior written permission.
#
# This software is provided by the copyright holders and contributors
# "as is" and any express or implied warranties, including, but not
# limited to, the implied warranties of merchantability and fitness
# for a particular purpose are disclaimed. In no event shall the
# copyright owner or contributors be liable for any direct, indirect,
# incidental, special, exemplary, or consequential damages (including,
# but not limited to, procurement of substitute goods or services;
# loss of use, data, or profits; or business interruption) however
# caused and on any theory of liability, whether in contract, strict
# liability, or tort (including negligence or otherwise) arising in
# any way out of the use of this software, even if advised of the
# possibility of such damage.
namespace Michelf;


# Just force Michelf/Markdown.php to load. This is needed to load
# the temporary implementation class. See below for details.
\Michelf\Markdown::MARKDOWNLIB_VERSION;

#
# Markdown Extra Parser Class
#
# Note: Currently the implementation resides in the temporary class
# \Michelf\MarkdownExtra_TmpImpl (in the same file as \Michelf\Markdown).
# This makes it easier to propagate the changes between the three different
# packaging styles of PHP Markdown. Once this issue is resolved, the
# _MarkdownExtra_TmpImpl will disappear and this one will contain the code.
#

class MarkdownExtra extends \Michelf\_MarkdownExtra_TmpImpl {

	### Parser Implementation ###

	# Temporarily, the implemenation is in the _MarkdownExtra_TmpImpl class.
	# See note above.

}

