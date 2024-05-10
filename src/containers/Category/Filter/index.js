import React, { useRef } from 'react';
import { TouchableOpacity, StyleSheet, View } from 'react-native';
import { BottomSheet, Text, TextField } from 'components';
import Colors from 'themes/colors';
import { scale } from 'react-native-size-matters';
import Icon from 'react-native-vector-icons/Feather';
import { getScreenHeight } from 'utils/size';
import FilterGroup from './FilterGroup';

const styles = StyleSheet.create({
  container: {
    flex: 1,
    padding: scale(14),
  },
  button: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  filter: {
    marginLeft: scale(5),
  },
  price: {
    flexDirection: 'row',
    flex: 1,
  },
  divider: {
    marginHorizontal: scale(10),
  },
  flex: {
    flex: 1,
  },
  label: {
    marginBottom: scale(10),
  },
});

const Filter = () => {
  const sheetRef = useRef(null);
  return (
    <>
      <TouchableOpacity style={styles.button} onPress={() => sheetRef.current.open()}>
        <Icon
          name="filter"
          color={Colors.white}
          size={scale(20)}
        />
        <Text color="white" font="p1" weight="medium" style={styles.filter}>Filter</Text>
      </TouchableOpacity>
      <BottomSheet sheetRef={sheetRef} buttonText="Show Results" height={getScreenHeight() / 1.2}>
        <View style={styles.container}>
          <FilterGroup title="Categories" options={['Sports', 'Shirts', 'Casual', 'Women', 'Men', 'Kids']} />
          <FilterGroup title="Rating" options={['5 Stars', '4 Stars & above', '3 Stars & above', '2 Stars & above', '1 Star & above']} />
          <FilterGroup title="Size Options" options={['Size XXS', 'Size XS', 'Size S', 'Size M', 'Size L', 'Size XL', 'Size XXL']} />
          <Text
            weight="medium"
            color="gray50"
            style={styles.label}
          >
            Price Range
          </Text>
          <View style={styles.price}>
            <View style={styles.flex}>
              <TextField label="Min. Price" />
            </View>
            <View style={styles.divider} />
            <View style={styles.flex}>
              <TextField label="Max. Price" />
            </View>
          </View>
        </View>
      </BottomSheet>
    </>
  );
};

export default Filter;
