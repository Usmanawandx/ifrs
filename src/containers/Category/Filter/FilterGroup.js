import React, { useState } from 'react';
import { View, StyleSheet } from 'react-native';
import { Text } from 'components';
import PropTypes from 'prop-types';
import { scale } from 'react-native-size-matters';
import FilterButton from './FilterButton';

const styles = StyleSheet.create({
  container: {
    marginBottom: scale(26),
  },
  group: {
    flexDirection: 'row',
    flexWrap: 'wrap',
  },
});

const FilterGroup = ({ title, options }) => {
  const [selected, setSelected] = useState([]);
  const onPress = (value) => {
    setSelected((prevValues) => {
      const values = [...prevValues];
      if (values.includes(value)) {
        values.splice(values.indexOf(value), 1);
        return values;
      }
      values.push(value);
      return values;
    });
  };

  return (
    <View style={styles.container}>
      <Text color="gray50" weight="medium">{title}</Text>
      <View style={styles.group}>
        {options.map((option) => (
          <FilterButton
            key={option}
            label={option}
            onPress={onPress}
            isSelected={selected.includes(option)}
          />
        ))}
      </View>
    </View>
  );
};

FilterGroup.propTypes = {
  title: PropTypes.string.isRequired,
  options: PropTypes.array.isRequired,
};

export default FilterGroup;
