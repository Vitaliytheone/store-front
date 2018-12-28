import React from "react";
import { SortableHandle } from "react-sortable-hoc";

export const DragHandle = SortableHandle(() => (
    <div className="sommerce_dragtable__category-move move">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
            <title>Drag-Handle</title>
            <path
                d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z"
                fill="#d4d4d4"
            />
        </svg>
    </div>
));

export default DragHandle;