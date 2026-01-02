#!/bin/bash

# Mapping emoji ke Font Awesome icons
declare -A emoji_map=(
    ["📗"]='<i class="fas fa-book"></i>'
    ["📚"]='<i class="fas fa-books"></i>'
    ["📊"]='<i class="fas fa-chart-bar"></i>'
    ["📈"]='<i class="fas fa-chart-line"></i>'
    ["📉"]='<i class="fas fa-chart-line-down"></i>'
    ["💰"]='<i class="fas fa-money-bill-wave"></i>'
    ["👥"]='<i class="fas fa-users"></i>'
    ["🎓"]='<i class="fas fa-graduation-cap"></i>'
    ["📝"]='<i class="fas fa-file-alt"></i>'
    ["📅"]='<i class="fas fa-calendar"></i>'
    ["✅"]='<i class="fas fa-check-circle"></i>'
    ["❌"]='<i class="fas fa-times-circle"></i>'
    ["⚠️"]='<i class="fas fa-exclamation-triangle"></i>'
    ["🔔"]='<i class="fas fa-bell"></i>'
    ["📢"]='<i class="fas fa-bullhorn"></i>'
    ["🏫"]='<i class="fas fa-school"></i>'
    ["📖"]='<i class="fas fa-book-open"></i>'
)

# Find and replace in all blade files
find resources/views -name "*.blade.php" -type f | while read file; do
    echo "Processing: $file"
    
    # Replace each emoji
    for emoji in "${!emoji_map[@]}"; do
        icon="${emoji_map[$emoji]}"
        # Use sed with proper escaping
        sed -i "s|$emoji|$icon|g" "$file"
    done
done

echo "Done! All emojis replaced with Font Awesome icons."
