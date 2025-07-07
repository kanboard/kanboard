<?php

namespace KanboardTests\units\User;

use KanboardTests\units\Base;
use Kanboard\User\DatabaseUserProvider;

class DatabaseUserProviderTest extends Base
{
    public function testGetInternalId()
    {
        $provider = new DatabaseUserProvider(array('id' => 123));
        $this->assertEquals(123, $provider->getInternalId());
    }
}
