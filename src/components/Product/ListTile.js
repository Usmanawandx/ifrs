import React from 'react';
import {
  TouchableOpacity, ImageBackground, StyleSheet, View,
} from 'react-native';
import { getScreenWidth } from 'utils/size';
import { scale } from 'react-native-size-matters';
import PropTypes from 'prop-types';
import Text from '../Text';
import Rating from '../Rating';

const styles = StyleSheet.create({
  container: {
    flexDirection: 'row',
    marginBottom: scale(14),
  },
  imageContainer: {
    width: getScreenWidth() / 3.5,
    aspectRatio: 1 / 1,
    borderRadius: scale(8),
    overflow: 'hidden',
  },
  row: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  info: {
    paddingHorizontal: scale(10),
    justifyContent: 'space-between',
    flex: 1,
  },
  bg: {
    flex: 1,
  },
  between: {
    justifyContent: 'space-between',
  },
  discount: {
    textDecorationLine: 'line-through',
    marginLeft: scale(8),
  },
});

const ListTile = ({
  name,
  price,
  sold,
  images,
  onPress,
  rating,
  beforeDiscount,
  numberOfReviews,
}) => (
  <TouchableOpacity
    style={styles.container}
    onPress={onPress}
  >
    <View style={styles.imageContainer}>
      <ImageBackground
        source={images[0]}
        style={styles.bg}
      />
    </View>

    <View style={styles.info}>
      <Text numberOfLines={2} weight="medium">{name}</Text>
      <View style={styles.row}>
        <Text color="tertiary" weight="medium" font="h3">{price}</Text>
        <Text color="gray50" style={styles.discount}>{beforeDiscount}</Text>
      </View>
      <View style={[styles.row, styles.between]}>
        <Rating rating={rating} total={numberOfReviews} size={12} />
        <Text color="gray50">{`${sold} sold`}</Text>
      </View>
    </View>
  </TouchableOpacity>
);

ListTile.propTypes = {
  name: PropTypes.string.isRequired,
  price: PropTypes.string.isRequired,
  sold: PropTypes.number.isRequired,
  images: PropTypes.array.isRequired,
  rating: PropTypes.number.isRequired,
  numberOfReviews: PropTypes.number.isRequired,
  beforeDiscount: PropTypes.string,
  onPress: PropTypes.func,
};

ListTile.defaultProps = {
  onPress: null,
  beforeDiscount: null,
};

export default ListTile;
