<div class="{__NODE_ID__} {DISABLED_CLASS}" instance="{__INSTANCE__}">

    <div class="bar" hover="hover" node_id="{NODE_ID}">
        <div class="container">
            <div class="info">
                <!-- required -->
                <div class="required">R</div>
                <!-- / -->
            </div>
            <div class="path">{PATH_TXT}</div>
            <div class="cb"></div>
        </div>

        <div class="cp hidden" node_id="{NODE_ID}">{CP}</div>
    </div>

    <!-- if node -->
    <div class="nodes">
        <!-- node -->
        <div class="node" node_{~NODE_ID}_node_id="{ID}" node_id="{ID}">
            {CONTENT}
        </div>
        <!-- / -->
    </div>
    <!-- / -->

</div>
